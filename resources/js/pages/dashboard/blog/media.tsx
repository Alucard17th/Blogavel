import { Head, useForm } from '@inertiajs/react';

import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';

type BlogavelProps = {
    admin_base: string;
};

type MediaRow = {
    id: number;
    original_name: string;
    mime_type: string;
    size: number;
    url: string;
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Blog',
        href: '/dashboard/blog',
    },
    {
        title: 'Media',
        href: '/dashboard/blog/media',
    },
];

export default function DashboardBlogMedia({
    blogavel,
    media,
}: {
    blogavel: BlogavelProps;
    media: MediaRow[];
}) {
    const uploadForm = useForm<{ file: File | null }>({
        file: null,
    });

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Blog - Media" />

            <div className="flex flex-1 flex-col gap-4 p-4">
                <Card className="py-0">
                    <CardHeader className="pt-6">
                        <CardTitle className="text-2xl">Media</CardTitle>
                        <CardDescription>
                            Upload and manage media (Blogavel admin)
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form
                            className="mb-6 flex flex-wrap items-end gap-3"
                            onSubmit={(e) => {
                                e.preventDefault();
                                uploadForm.post('/dashboard/blog/media', {
                                    forceFormData: true,
                                });
                            }}
                        >
                            <div className="grid gap-2">
                                <Label htmlFor="file">Upload image</Label>
                                <Input
                                    id="file"
                                    type="file"
                                    accept="image/*"
                                    onChange={(e) =>
                                        uploadForm.setData(
                                            'file',
                                            e.target.files?.[0] ?? null,
                                        )
                                    }
                                />
                                {uploadForm.errors.file ? (
                                    <div className="text-sm text-destructive">
                                        {uploadForm.errors.file}
                                    </div>
                                ) : null}
                            </div>

                            <Button type="submit" disabled={uploadForm.processing}>
                                Upload
                            </Button>
                        </form>

                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead className="w-[90px]">ID</TableHead>
                                    <TableHead>File</TableHead>
                                    <TableHead className="hidden md:table-cell">
                                        MIME
                                    </TableHead>
                                    <TableHead className="hidden md:table-cell text-right">
                                        Size
                                    </TableHead>
                                    <TableHead className="w-[160px]" />
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {media.length === 0 ? (
                                    <TableRow>
                                        <TableCell colSpan={5}>
                                            <div className="py-6 text-center text-sm text-muted-foreground">
                                                No media found.
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    media.map((m) => (
                                        <TableRow key={m.id}>
                                            <TableCell className="font-mono text-xs">
                                                {m.id}
                                            </TableCell>
                                            <TableCell>
                                                <div className="font-medium">
                                                    {m.original_name}
                                                </div>
                                                <a
                                                    href={m.url}
                                                    target="_blank"
                                                    rel="noreferrer"
                                                    className="text-xs text-muted-foreground underline"
                                                >
                                                    View
                                                </a>
                                            </TableCell>
                                            <TableCell className="hidden md:table-cell">
                                                <span className="font-mono text-xs">
                                                    {m.mime_type}
                                                </span>
                                            </TableCell>
                                            <TableCell className="hidden md:table-cell text-right">
                                                {(m.size / 1024).toFixed(0)} KB
                                            </TableCell>
                                            <TableCell>
                                                <div className="flex items-center gap-2">
                                                    <Button asChild variant="outline" size="sm">
                                                        <a
                                                            href={m.url}
                                                            target="_blank"
                                                            rel="noreferrer"
                                                        >
                                                            Open
                                                        </a>
                                                    </Button>
                                                    <Button
                                                        variant="destructive"
                                                        size="sm"
                                                        onClick={() =>
                                                            uploadForm.delete(
                                                                `/dashboard/blog/media/${m.id}`,
                                                            )
                                                        }
                                                    >
                                                        Delete
                                                    </Button>
                                                </div>
                                            </TableCell>
                                        </TableRow>
                                    ))
                                )}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
