import { Link } from "@inertiajs/react";

interface Props {
	status: number;
}

const title: Record<number, string> = {
	503: "Layanan Tidak Tersedia",
	500: "Kesalahan Server",
	404: "Halaman Tidak Ditemukan",
	403: "Akses Ditolak",
	419: "Halaman Kadaluarsa",
} as const;

const description: Record<number, string> = {
	503: "Maaf, kami sedang melakukan pemeliharaan. Silakan periksa kembali nanti.",
	500: "Oops, terjadi kesalahan pada server kami.",
	404: "Maaf, halaman yang Anda cari tidak dapat ditemukan.",
	403: "Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.",
	419: "Sesi Anda telah berakhir. Silakan muat ulang halaman dan coba lagi.",
} as const;

export default function ErrorPage({ status }: Props) {
	return (
		<div className="flex items-center min-h-screen px-4 py-12 sm:px-6 md:px-8 lg:px-12 xl:px-16">
			<div className="w-full space-y-6 text-center">
				<div className="space-y-3">
					<h1 className="text-4xl font-bold tracking-tighter sm:text-5xl transition-transform hover:scale-110">
						{title[status]}
					</h1>
					<p className="text-gray-900">{description[status]}</p>
				</div>
				<Link
					href="/"
					className="inline-flex h-10 items-center rounded-md bg-gray-900 px-8 text-sm font-medium text-gray-50 shadow transition-colors hover:bg-gray-900/90 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-gray-950 disabled:pointer-events-none disabled:opacity-50 dark:bg-gray-50 dark:text-gray-900 dark:hover:bg-gray-50/90 dark:focus-visible:ring-gray-300"
					prefetch={false}
				>
					Kembali ke halaman utama
				</Link>
			</div>
		</div>
	);
}
