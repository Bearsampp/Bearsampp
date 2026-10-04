<#
    .SYNOPSIS
    Walks a Bearsampp install and reports its on-disk size, grouped by part.

    .DESCRIPTION
    Prints one compact JSON object on stdout for status-disk-usage.php to decode.
    Nothing else may reach stdout, so every diagnostic goes to stderr.

    Grouping is one level deep on purpose. The top level answers "which part of
    the install is large", and the children answer "which version of it", which
    is the question that matters in bin/ where superseded releases accumulate.
    A deeper tree would mostly add noise at a cost paid per file.

    Two behaviours of the traversal are load-bearing:

      1. Get-ChildItem -Recurse does not descend into directory symlinks or
         junctions. That is what keeps bin/php/current and friends from being
         counted twice, which would report roughly 26 GB for an 18.9 GB install.
         A plain dir /s does follow them and must not be used for this.
      2. Files sitting directly in the root are grouped under a synthetic part,
         so that the sum of the parts is exactly the reported total. A total
         that does not equal the sum of its parts is a figure nobody can verify
         at a glance.
      3. Six named entries are left out wherever they appear: the .git, .idea,
         .github and .junie directories, and the .gitignore and .htaccess files.
         The question being answered is how much room the install needs, and
         version control metadata, editor state and assistant scratch space answer
         a different one: .git alone is 354 MB of pack files that a user cannot
         remove to free space. The list is a closed denylist on purpose. Only these
         names are asked for, so other dot entries stay in the figure: a file like
         .packlist or .gitattributes is as much a part of the install as any other.

    That synthetic part is flagged with isRootFiles rather than being recognised
    by its name, so that the page can label it in the user's language without
    depending on the text this script happens to use.

    .PARAMETER Root
    The Bearsampp install directory to measure. Both slash styles are accepted.
#>
param(
    [Parameter(Mandatory = $true)]
    [string]$Root
)

# SilentlyContinue covers unreadable paths, which are expected here: a running
# database holds locked files, and a permission-denied subtree should cost its
# own files rather than abort the whole measurement.
$ErrorActionPreference = 'SilentlyContinue'

# Normalised to backslashes because the comparison below is what decides whether
# a file is counted at all. Path::getRootPath() hands back a forward-slash path
# such as D:/sandbox, while Get-ChildItem always reports D:\sandbox\..., so
# without this the prefix test fails for every file and the walk reports a
# plausible-looking zero instead of an error. Comparing case-insensitively is
# equally load-bearing for the same reason.
$Root = $Root.Replace('/', '\')

if (-not (Test-Path -LiteralPath $Root)) {
    [Console]::Error.WriteLine("Root does not exist: $Root")
    Write-Output '{"error":"Root does not exist."}'

    return
}

$prefix = $Root.TrimEnd('\') + '\'
$parts  = @{}

# Matches only a full path segment that is one of the excluded names, so a leading
# entry and a nested one both count while a longer name that merely starts with the
# same text does not. Compiled once because it runs per file.
$excludedNames = @('.gitignore', '.htaccess', '.github', '.junie', '.idea', '.git')
$excludedAlternation = ($excludedNames | ForEach-Object { [regex]::Escape($_) }) -join '|'
$excludedSegment = [regex]::new(
    '(^|\\)(' + $excludedAlternation + ')(\\|$)',
    [System.Text.RegularExpressions.RegexOptions]::IgnoreCase
)

# Get-ChildItem is the slowest part of this script by a wide margin, about 7s for
# a 230k file install on an SSD, and that floor is the traversal itself rather
# than the accounting below. Rewriting the accumulation in plain hashtables
# measured within noise of the PSCustomObject version, so the clearer form is
# kept. This is why the caller caches the result instead of computing it on
# every page load.
Get-ChildItem -LiteralPath $Root -Recurse -File -Force | ForEach-Object {
    $full = $_.FullName

    if ($full.Length -le $prefix.Length) {
        return
    }

    if (-not $full.StartsWith($prefix, [System.StringComparison]::OrdinalIgnoreCase)) {
        return
    }

    $relative = $full.Substring($prefix.Length)

    # A path is skipped when any of its segments is exactly one of the names above.
    # The trailing boundary is what keeps .gitattributes and .git-tmp in the figure
    # while .git drops out, since both begin with the same four characters as .git.
    # Segment boundaries are matched inline rather than by splitting the path, which
    # would allocate an array per file across 230k files for no gain. This is a
    # filter on the results rather than a prune of the walk, deliberately: skipping
    # .git removes 354 MB from the figure but only 136 files, well under 1% of the
    # traversal, so pruning would add a hand-rolled recursion and re-open the
    # junction handling that item 1 above depends on.
    if ($excludedSegment.IsMatch($relative)) {
        return
    }

    $separator = $relative.IndexOf('\')

    if ($separator -lt 0) {
        $top       = '(root files)'
        $sub       = ''
        $isRootFiles = $true
    }
    else {
        $top        = $relative.Substring(0, $separator)
        $remainder  = $relative.Substring($separator + 1)
        $nested     = $remainder.IndexOf('\')
        $sub        = if ($nested -lt 0) { '' } else { $remainder.Substring(0, $nested) }
        $isRootFiles = $false
    }

    if (-not $parts.ContainsKey($top)) {
        $parts[$top] = [pscustomobject]@{
            name        = $top
            isRootFiles = $isRootFiles
            bytes       = [int64]0
            files       = [int64]0
            children    = @{}
        }
    }

    $part = $parts[$top]
    $part.bytes += $_.Length
    $part.files++

    if ($sub -ne '') {
        if (-not $part.children.ContainsKey($sub)) {
            $part.children[$sub] = [pscustomobject]@{
                name  = $sub
                bytes = [int64]0
                files = [int64]0
            }
        }

        $part.children[$sub].bytes += $_.Length
        $part.children[$sub].files++
    }
}

$ordered = @($parts.Values | Sort-Object -Property bytes -Descending)

# The children are moved out of their hashtable and the hashtable itself is
# dropped, because ConvertTo-Json in Windows PowerShell 5.1 serialises an empty
# or single-entry hashtable inconsistently. Always materialising a list keeps the
# JSON shape stable for the decoder.
foreach ($part in $ordered) {
    $children = @($part.children.Values | Sort-Object -Property bytes -Descending)
    $part | Add-Member -NotePropertyName childrenList -NotePropertyValue $children -Force
    $part.PSObject.Properties.Remove('children')
}

[pscustomobject]@{
    root  = $Root
    parts = $ordered
    total = [pscustomobject]@{
        bytes = [int64](($ordered | Measure-Object -Property bytes -Sum).Sum)
        files = [int64](($ordered | Measure-Object -Property files -Sum).Sum)
    }
} | ConvertTo-Json -Depth 5 -Compress
